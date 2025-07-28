# Utiliser PHP 8.3 avec Apache
FROM php:8.3-cli

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers de l'application
COPY . .

# Installer les dépendances Composer et regénérer l'autoload
RUN composer install --no-dev --optimize-autoloader
RUN composer dump-autoload --optimize

# Exposer le port
EXPOSE $PORT

# Commande de démarrage
CMD php -S 0.0.0.0:$PORT -t public
