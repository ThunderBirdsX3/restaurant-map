# To start the project

`bash sail up -d`

wait for a minute to let `composer install` run completely

then run 

`bash sail npm i && bash sail npm run build`

Don't forget to set `GOOGLE_MAP_KEY` in `.env` file

Open http://127.0.0.1:8080/
