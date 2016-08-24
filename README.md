# Getting started with Lo-module-gallery

## Update database schema

The last thing you need to do is updating your database schema by applying the
migrations. Make sure that you have properly configured `db` application component
and run the following command:

```bash
$ php yii migrate/up --migrationPath=@vendor/loveorigami/lo-module-gallery/migrations
$ php yii migrate/down --migrationPath=@vendor/loveorigami/lo-module-gallery/migrations
```

## Create database schema
```bash
$ php yii migrate/create --migrationPath=@vendor/loveorigami/lo-module-gallery/migrations "gallery_"
```
