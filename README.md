## Insee Zadanie testowe

# Instalacja

 - 1) Klonowanie repozytorium: git clone https://github.com/paulpiotr/insee.git
 - 2) Uprawnienia w linux (użytkownik i grupa): cd ..; sudo chown www-data:[twój login] insee -R
 - 3) Uprawnienia w linux (uprawnienia): sudo chmod 775 insee -R
 - 4) Instalowanie bibliotek: cd insee; composer update

 # Comendy
 
 - 1) Pomoc: php bin/console app:last-commit --help
 - 2) Przykładowa komenda: php bin/console app:last-commit paulpiotr/insee master
 