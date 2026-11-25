---
trigger: model_decision
description: "Aplicar ao alterar Laravel, Filament, banco, tarefas agendadas ou deploy Plesk."
---
Use migrations com FKs e índices; IDs Z-API são strings opacas, nunca telefones normalizados. UTC no banco, America/Sao_Paulo na UI. Preserve histórico e snapshots; não apague mídia referenciada. Eloquent com eager loading, paginação, sem polling contínuo. Cron limitado, locks com cache compartilhado, falha parcial não remove grupos. Consulte docs/DATABASE.md e docs/PLESK.md.
