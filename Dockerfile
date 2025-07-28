# Utiliser PHP 8.3 avec Apache
FROM php:8.3-cli

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers de l'application
COPY . .

# Installer les dépendances Composer
RUN composer install --no-dev --optimize-autoloader

# Exposer le port
EXPOSE $PORT

# Commande de démarrage
CMD php -S 0.0.0.0:$PORT -t public
