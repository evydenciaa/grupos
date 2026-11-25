# Evydência Grupos — instruções para agentes
Objetivo: painel pt-BR para administrar grupos WhatsApp, mídia e legendas, e revisar publicações autorizadas via Z-API. Laravel 13, Filament 5, PHP 8.3/8.4, MySQL/MariaDB, Plesk compartilhado.
## Antes de editar
- Leia README.md e docs/STATUS.md; confira git status e composer.lock quando existir.
- Selecione apenas a skill relevante em .agents/skills. Consulte a documentação da versão instalada, não exemplos antigos.
- Nunca modifique o CRM externo, credenciais ou produção. Não faça envios reais em desenvolvimento, CI ou testes. Use Http::fake e preventStrayRequests.
- Grupos novos começam desabilitados; regras e autorização explícita prevalecem. Não coletar membros individualmente, não usar rotação para evadir bloqueios.
- Sem Redis, Horizon, Supervisor, Docker ou Node em produção; somente public/ exposto. Nenhum token, link privado ou dado de cliente no Git.
## Fluxo
1. Reproduza e escreva testes; audite contratos externos.
2. Implemente a menor mudança, usando Resources e Eloquent nativos. Não crie SPA.
3. Execute Pint, migrations e testes, inclusive MySQL. Verifique interface mobile e permissões.
4. Atualize docs/STATUS.md com evidência, pendências e comandos executados; não declare testes que não rodaram.
5. Commit pequeno, sem força ou deploy automático.
