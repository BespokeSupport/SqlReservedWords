# SQL Reserve Words checker

## Examples
```
sql-reserved-words.phar check example/ mysql57

OK      | Example\Group
OK      | Example\NoErrors

sql-reserved-words.phar check example/ mysql

ERR     | Example\Group
          'Errors' class name is a keyword for 'mysql80'
OK      | Example\NoErrors

sql-reserved-words.phar check example/ mysql,pgsql

ERR     | Example\Group
          'Errors' class name is a keyword for 'mysql80'
          'Errors'::`user` is a keyword for 'pgsql91,pgsql92,pgsql94,pgsql10
OK      | Example\NoErrors

sql-reserved-words.phar check example/ pgsql

ERR     | Example\Group
          'Errors'::`user` is a keyword for 'pgsql91,pgsql92,pgsql94,pgsql10
OK      | Example\NoErrors

```