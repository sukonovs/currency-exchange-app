## Reqs
Docker, Docker compose, Makefile, Git (for git clone), cron

## Installation
1) Clone/Download repo
2) run ``make build`` to build containers
2) run ``make setup`` to setup project
3) run ``make fetch`` to initially fetch feed
4) open http://localhost:8000 
5) run ``make end`` for cleanup

## Crontab
Add fetching to crontab ``* * * * * docker-compose -f [PATH TO CLONED REPO]/docker-compose.yml run app bin/console app:fetch-fx-rates --env=prod``