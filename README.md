Acl Plugin sample
=======

## How to setup this application

    $ git clone https://github.com/kaz29/acl_plugin_sample.git
    $ cd acl_plugin_sample
    $ chmod -R go+w ./app/tmp
    $ cp ./app/Config/database.php.default ./app/Config/database.php
    $ vim ./app/Config/database.php

        setup your database ...

    $ mysql [your database name] < schema_mysql.sql

        OR

    $ psql [your database name] < schema_postgres.sql
