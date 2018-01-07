[![Build Status](https://travis-ci.org/yasiekz/payments-DDD-ES-tutorial.svg?branch=master)](https://travis-ci.org/yasiekz/payments-DDD-ES-tutorial)
[![codecov](https://codecov.io/gh/yasiekz/payments-DDD-ES-tutorial/branch/master/graph/badge.svg)](https://codecov.io/gh/yasiekz/payments-DDD-ES-tutorial)

# DDD + CQRS + ES + Symfony 4 based API + docker + Codeception

This is tutorial project to learn how to connect Symfony4 and docker compose with Domain Driven Design and Event Sourcing

Project resolves real problem with bank accounts and payments. **Under development**

Event Souring is based on two most popular PHP libraries:
- https://github.com/prooph
- https://github.com/broadway/broadway

#### Key features:
- fully dockerized project
- payments and account balance build from events
- around 90% code coverage with phpunit unit and functional tests
- event store on mongoDB, fully separated from domain
- travis automated tests

#### Scheduled functionality
- AccountBalanceRepository
- Load domain objects from snapshots
- REST API calls with CQRS
- API tests with Codeception

### Local run

You need docker and docker-compose installed on your system.
After cloning repo just run:

```
docker-compose up -d
docker exec -it payments-ddd-es-tutorial-php composer install
```

Then on ```localhost:8080``` will be your site.

### Run automated tests

```
docker-compose up -d
docker exec -it payments-ddd-es-tutorial-php vendor/bin/phpunit -c phpunit.xml.dist
```

