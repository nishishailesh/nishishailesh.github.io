#!/bin/bash
echo 'Give mysql user'
read user
mysqldump   -h127.0.0.1 -u$user -p food > food_blank.sql 
git add *
git commit -a
git push https://github.com/nishishailesh/food main
