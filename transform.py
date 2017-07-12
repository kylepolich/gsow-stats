import pandas as pd
import sqlalchemy
import ConfigParser
import time
import datetime
import urllib

propertiesFile = "my.properties"
cp = ConfigParser.ConfigParser()
cp.readfp(open(propertiesFile))

db_host     = cp.get('Params', 'db_host')
db_user     = cp.get('Params', 'db_user')
db_password = cp.get('Params', 'db_password')
db_db       = cp.get('Params', 'db_db')

#conn = mdb.connect(host=db_host, user=db_user, passwd=db_password, db=db_db, charset = 'utf8')
conn = sqlalchemy.create_engine("mysql://{}:{}@{}:{}/{}".format(db_user, db_password, db_host, 3306, db_db))

query = """
select distinct t1.pageid, t1.lang, t1.page, t1.start, lower(t2.tag) as tag
from edits t1
join tags t2
 on t1.pageid = t2.pageid
"""

df = pd.read_sql(query, conn)
df['c'] = 'x'
df['ind'] = df['page'] + '(' + df['lang'] + ')'# + df['start']
df2 = df.pivot(index='ind', columns='tag', values='c')
df2.fillna('', inplace=True)

df2.columns = map(lambda x: x.replace(' ', '_'), df2.columns.tolist())
df2.reset_index(inplace=True)

df2.to_sql('pivot', conn, if_exists='replace')

tbl = df2.to_html()

f = open('pivot.htm', 'w')
f.write(tbl.encode('utf-8'))
f.close()
