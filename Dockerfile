# First, run compose install
FROM git-itcs.yogya.com:5100/docker-images/composer-custom:latest AS composer

ARG ENV_KEY
ARG APP_VERSION_HASH
ARG ENV_TYPE=staging

RUN mkdir -p /var/www/localhost

COPY . /var/www/localhost

WORKDIR /var/www/localhost

RUN echo "APP_VERSION_HASH=${APP_VERSION_HASH}" >> .constants && \
    composer install --ignore-platform-reqs --optimize-autoloader --no-dev --no-interaction --no-progress --prefer-dist && \
    php artisan env:decrypt --env=${ENV_TYPE} --key=${ENV_KEY} || true && \
    ln -sf .env.${ENV_TYPE} .env || true && \
    ls -lah .env*

# Second, run npm install
FROM git-itcs.yogya.com:5100/docker-images/npm-custom:latest-ns AS npm

COPY --from=composer /var/www/localhost /var/www/localhost

WORKDIR /var/www/localhost

RUN npm install && \
    npx vite build

# Third, run Laravel install
FROM git-itcs.yogya.com:5100/docker-images/php:8.3-frankenphp

RUN echo "upload_max_filesize=20M" > /etc/php/8.3/cli/conf.d/uploads.ini && \
    echo "post_max_size=25M" >> /etc/php/8.3/cli/conf.d/uploads.ini

COPY --from=npm /var/www/localhost /var/www/localhost

WORKDIR /var/www/localhost

RUN mkdir -p /var/www/images \
    && chown -R www-data:www-data /var/www/images \
    && chmod -R 775 /var/www/images

RUN mkdir -p bootstrap/cache \
    && chown -R www-data:www-data bootstrap/cache storage \
    && chmod -R 775 bootstrap/cache storage

RUN php artisan octane:install --server=frankenphp

RUN php artisan storage:link

RUN mkdir -p /var/www/localhost/storage/framework/sessions \
    && mkdir -p /var/www/localhost/storage/framework/views \
    && mkdir -p /var/www/localhost/storage/framework/cache

RUN chmod -R 775 /var/www/localhost/storage/framework

RUN rm -rf rm -rf node_modules .pnpm-store public/debug.php resources/css resources/fonts resources/images resources/js resources/vue stubs tests cypress .git .github .gitlab .gitattributes .gitignore .vscode .editorconfig .styleci.yml .eslintignore .eslintrc.js .phpunit.result.cache .stylelintrc.json package.json package-lock.json pint.json tsconfig.json tsconfig.node.json *.yaml *.md *.lock *.xml *.yml *.ts *.jsyml *.ts *.js *.sh .browserslistrc .devcontainer.json .eslintrc.cjs phpunit.xml.dist postcss.config.cjs tailwind.config.cjs *.config.mjs phpunit.xml.dist postcss.config.cjs tailwind.config.cjs Jenkinsfile*

RUN rm -rf /var/www/localhost/Dockerfile && \
    ls -lah /var/www/localhost && \
    cd /var/www/localhost

VOLUME ["/var/www/localhost/storage"]
