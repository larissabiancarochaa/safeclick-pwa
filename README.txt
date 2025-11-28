SafeClick PWA - README

Rodar local:
php -S 0.0.0.0:8080

Abra http://localhost:8080

Para transformar em APK (TWA):
- Gere icons 192x192 e 512x512 em /public/
- Use PWABuilder ou peça ao DeepSeek para empacotar em TWA/Android
- Verifique HTTPS em produção (TWA exige site servido por HTTPS)

Funcionalidades:
✓ CRUD completo de URLs
✓ Análise heurística de segurança
✓ Histórico local em JSON
✓ Interface responsiva
✓ PWA installable
✓ Service Worker offline
✓ Arquitetura MVC