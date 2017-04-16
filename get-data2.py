import requests
import MySQLdb as mdb
import pandas as pd
import numpy as np
import json
import ConfigParser
import time
import datetime
import urllib
from dateutil.relativedelta import relativedelta
from mwviews.api import PageviewsClient

propertiesFile = "my.properties"
cp = ConfigParser.ConfigParser()
cp.readfp(open(propertiesFile))

db_host     = cp.get('Params', 'db_host')
db_user     = cp.get('Params', 'db_user')
db_password = cp.get('Params', 'db_password')
db_db       = cp.get('Params', 'db_db')

conn = mdb.connect(host=db_host, user=db_user, passwd=db_password, db=db_db, charset = 'utf8')

cache = {}

# Handle brand new pages added which haven't had anything downloaded yet.  Determine pageid

query = "select * from edits where pageid is null"
df = pd.read_sql(query, conn)

p = PageviewsClient()

q = "UPDATE edits set pageid={} WHERE edit_id={}"
cur = conn.cursor()
for r in range(df.shape[0]):
  row = df.iloc[r]
  editid = row['edit_id']
  title = row['page']
  lang = row['lang']
  url = 'https://' + lang + '.wikipedia.org/w/api.php?action=query&format=json&titles=' + urllib.quote(title.encode("utf8"))
  print 'Getting', url
  req = requests.get(url)
  j = json.loads(req.text)
  try:
    pages = j['query']['pages']
  except:
    print 'Error on ', r, title, url
  else:
    keys = pages.keys()
    if len(keys)==1:
      key = keys[0]
      pg = pages[key]
      if pg.has_key('pageid'):
        pageid = pg['pageid']
        q2 = q.format(pageid, editid)
        cur.execute(q2)
        conn.commit()
      else:
        print "Missing page: " + key

cur.close()




# UPDATE PAGE VIEWS

query = """
    SELECT t1.pageid, t1.lang, t1.page as title, t1.start
     , min(t2.dt) as first_dt
     , max(t2.dt) as last_dt
    FROM edits t1
    LEFT JOIN page_views t2
     ON t1.pageid = t2.pageid
    WHERE t1.pageid is not null
    AND t1.start <> '0000-00-00'
    or t1.pageid in (
	    select t1.pageid
			from edits t1
			left join page_views t2 on t1.pageid = t2.pageid
			where t2.pageid is null
		)
    group by t1.pageid, t1.lang, t1.page, t1.start
    having max(coalesce(t2.dt, '2000-01-01')) < DATE_FORMAT(DATE_ADD(now(), INTERVAL -2 DAY), '%Y-%m-%d')
"""

cur = conn.cursor()
cur.execute("SET NAMES utf8")
df2 = pd.read_sql(query, conn)
cur.close()

query = "INSERT INTO page_views (pageid, project, dt, views) VALUES({}, '{}', '{}', {}) ON DUPLICATE KEY UPDATE views=VALUES(views)"

# TODO: only get 90 if we have nothing, otherwise get less

nnow = datetime.datetime.utcnow()

print nnow
nday = nnow.day

cur = conn.cursor()
cur.execute("SET NAMES utf8")
for r in range(df2.shape[0]):
    row = df2.iloc[r]
    last_dt = row['last_dt']
    first_dt = row['first_dt']
    start = row['start']
    project = row['lang']
    # Pandas has the right encoding, but assigning it errors it
    title = row['title'].strip()
    if last_dt is None:
        last_dt = start#datetime.datetime.now() - datetime.timedelta(365*10,0)
    else:
        last_dt = datetime.datetime.combine(row['last_dt'], datetime.time(0))
    if first_dt is not None and first_dt > start.to_datetime().date():
        last_dt = start.to_datetime()
    current = last_dt
    st = str(last_dt.year) + str(last_dt.month).zfill(2) + str(last_dt.day).zfill(2)
    end = str(nnow.year) + str(nnow.month).zfill(2) + str(nnow.day).zfill(2)
    articles = [title.encode('utf8')]
    print 'Getting', articles, project, 'from', st, 'to', end
    try:
        resp = p.article_views(project + '.wikipedia', articles, start=st, end=end)
        pageid = row.pageid
        for dv in resp.keys():
            pv = resp[dv][title.encode('utf8').replace(' ', '_')]
            if pv is None:
              rdt = resp[dv]
              alts = rdt.keys()
              alts.remove(articles[0])
              if len(alts) > 0:
                pv = rdt[alts[0]]
            if pv is not None:
              q = query.format(pageid, project, dv, pv)
              res = cur.execute(q)
              conn.commit()
    except:
      print 'Unable to get', articles, project, 'from', st, 'to', end
      time.sleep(.1)

"""
    nnow = datetime.datetime.combine(nnow, datetime.time(0))
    cday = current.day
    nnow = nnow - relativedelta(days=nnow.day-1)
    current = current - relativedelta(days=current.day-1)
    #print title, current, nnow, cday, nday
    c = 0
    while current < nnow or (current.year==nnow.year and current.month==nnow.month and (cday < nday-1 or c>0)):
"""

cur.close()


