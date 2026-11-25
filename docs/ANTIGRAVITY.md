# Antigravity — guia operacional (25/11/2026)
Referências oficiais:
- https://antigravity.google/docs/changelog?tab=hub
- https://antigravity.google/docs/skills
- https://antigravity.google/docs/rules/
- https://antigravity.google/docs/subagents
- https://antigravity.google/docs/migration/workflows-to-skills/
- https://antigravity.google/docs/dynamic-workflows
- https://filamentphp.com/docs/5.x/introduction/ai
- https://github.com/laravel/boost

A documentação atual usa .agents/skills e .agents/agents. Workflows Markdown legados estão depreciados; não duplicar instruções. Use /grupos-bootstrap, /grupos-audit, /grupos-zapi, /grupos-panel, /grupos-database ou /grupos-plesk. No App/CLI compatível, use /run-workflow para revisão paralela, sem editar o mesmo arquivo simultaneamente.
Prompt recomendado: "/run-workflow Audite em paralelo o contrato Z-API, a modelagem e a interface Filament. Cada subagente deve produzir achados com arquivo e linha, sem alterações. Consolide, implemente sequencialmente as correções, execute CI local e reporte evidências. Sem chamadas reais de envio."
Em instalação de desenvolvimento: composer require laravel/boost --dev; php artisan boost:install. Se Antigravity não estiver entre os clientes detectados, confira a configuração MCP documentada para a versão instalada e selecione as skills oficiais; não invente flags nem sobrescreva skills deste repositório. Nunca instalar Boost em produção.
