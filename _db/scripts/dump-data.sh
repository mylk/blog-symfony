#!/bin/bash
# add --skip-extended-insert=true to get rid of extended insert statements
mysqldump --default-character-set=utf8 --no-create-info -uroot -p blog > ../blog.init-data.sql
