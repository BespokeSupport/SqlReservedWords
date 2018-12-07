# SQL Reserve Words checker

## Examples
```
sql-reserved-words.phar check example/ mysql57

OK      | Example\Errors
OK      | Example\NoErrors

sql-reserved-words.phar check example/ mysql

ERR     | Example\Errors
          'Errors' class name is a keyword for 'mysql80'
OK      | Example\NoErrors

sql-reserved-words.phar check example/ mysql,pgsql

ERR     | Example\Errors
          'Errors' class name is a keyword for 'mysql80'
          'Errors'::`user` is a keyword for 'pgsql91,pgsql92,pgsql94,pgsql10
OK      | Example\NoErrors

sql-reserved-words.phar check example/ pgsql

ERR     | Example\Errors
          'Errors'::`user` is a keyword for 'pgsql91,pgsql92,pgsql94,pgsql10
OK      | Example\NoErrors

```