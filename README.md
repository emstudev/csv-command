Setup guide:
1. Go to project root directory
2. In terminal run: chmod +x ./docker-env-install.sh
3. In terminal run: ./docker-env-install.sh - this will set up everything for you, run migrations etc
5. As per requirements you have following:
Commands:
```
docker exec -ti symfony_php php bin/console app:import-stocks /var/www/html/public/Resources/Csv/trah.csv trah
```
```
docker exec -ti symfony_php php bin/console app:import-stocks /var/www/html/public/Resources/Csv/lorotom.csv lorotom
```
API
```
GET (http://localhost:8080/api/get-stock?mpn=MR10&ean=5907555340603)
```
