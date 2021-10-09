# TK 1 SOA PHP Server

Language: PHP

Database: MySQL (ClearDB)

Deployed on heroku: https://young-plateau-37479.herokuapp.com/

## Start Locally

```sh
  git clone https://github.com/vyonizr/tk-1-soa-php-server.git
  cd tk-1-soa-php-server
  php -S localhost:8080 -t web
```

## Routes

| Route                 | HTTP | Params | Body | Description                             |
| --------------------- | ---- | ------ | ---- | --------------------------------------- |
| `couriers`            | GET  | -      | -    | Get all couriers list                   |
| `couriers/:courierId` | GET  | -      | -    | Get all shipment options from a courier |
