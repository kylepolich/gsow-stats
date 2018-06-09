
insert into tag_group
select distinct t1.tag, 'Editors'
from tags t1
left join tag_group t2
   on t1.tag=t2.tag
where t1.tag like 'Editor%%'
and t2.tag_group is null

insert into tag_group
select distinct t1.tag, 'Language'
from tags t1
left join tag_group t2
   on t1.tag=t2.tag
where t1.tag like 'Language%%'
and t2.tag_group is null

