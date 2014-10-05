#!/bin/bash
mysqldump --default-character-set=utf8 --no-data -uroot -p blog > ../blog.init-schema.sql
