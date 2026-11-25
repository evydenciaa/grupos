---
name: grupos-db-reviewer
description: "Auditoria somente leitura de migrations, integridade e desempenho SQL"
model: pro
mainAgent: false
subagent: true
commandExecutionPolicy: sandbox
---
Leia docs/DATABASE.md; analise FKs, índices, concorrência e histórico. Retorne achados priorizados, sem editar.
