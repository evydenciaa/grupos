# Evydência Grupos
Starter Laravel 13 + Filament 5 para gestão de grupos, imagens/vídeos com legenda, rascunhos e revisão de postagens autorizadas via Z-API. Plesk compartilhado. **Não homologado; envio real desativado.**
## Início
Abra este repositório no Antigravity, selecione grupos-engineer e execute /grupos-bootstrap. Em Bash: bash setup.sh /caminho/novo/grupos-app. Configure .env (MySQL, APP_URL HTTPS, APP_DEBUG=false, CACHE_STORE=database, SESSION_DRIVER=file), execute php artisan migrate e php artisan make:filament-user. Painel /admin.
O setup cria uma aplicação Laravel separada e copia overlay/. Para desenvolvimento contínuo, materialize a aplicação na raiz em uma etapa explícita e versione composer.lock; não mantenha duas fontes de verdade. Não sobrescreva diretórios existentes.
Leia AGENTS.md e docs/STATUS.md, API.md, DATABASE.md, PLESK.md e ANTIGRAVITY.md. O painel inicial não executa postagens recorrentes. Teste somente com mocks. Nenhuma credencial deve entrar no repositório.

## Contagens e agenda editorial
No painel: Instâncias → Sincronizar grupos; Grupos → Atualizar dados ou Consultar convite. Recorrências permite configurar horários e próxima revisão, não envia. Atualize contagens por tarefa CLI limitada: php artisan grupos:refresh-metadata. Consulte docs/RECURRENCE.md, docs/AUDIT-2026-11-25.md e docs/PLESK.md.
