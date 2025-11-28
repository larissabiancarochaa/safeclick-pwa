FROM php:8.2-apache

# Habilitar apenas o mod_rewrite (PHP já está integrado)
RUN a2enmod rewrite

# Configurar ServerName para evitar warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Criar diretório da aplicação
WORKDIR /var/www/html

# Copiar arquivos da aplicação
COPY . .

# Configurar permissões
RUN mkdir -p data && \
    chown -R www-data:www-data /var/www/html/ && \
    chmod -R 755 /var/www/html/ && \
    chmod 666 data/*.json 2>/dev/null || true

# Criar arquivos JSON se não existirem
RUN if [ ! -f data/links.json ]; then echo "[]" > data/links.json; fi && \
    if [ ! -f data/blacklist.json ]; then echo '["malicious.example","phish.test","bad.domain"]' > data/blacklist.json; fi

EXPOSE 80