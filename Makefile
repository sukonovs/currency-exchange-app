build:
	docker-compose up --build -d

end:
	docker-compose down --volumes --rmi local

# app has www-data user (PHP-FPM builds it that way), so by adding current user to www-data group we can
# avoid painful permission confilcts between container and host
# NB: only for linux on MAC everything works fine without this
fix-perm-linux:
	docker-compose exec app chown -R www-data:www-data .
	sudo chmod -R ug+rw .