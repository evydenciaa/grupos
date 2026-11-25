# Modelo relacional
Fundação: instances 1:N whatsapp_groups; contents 1:N publications; whatsapp_groups 1:N publications; publications 1:N publication_events; users 1:N approvals.
Unicidade: instances.external_id; groups(instance_id,external_id); publications(group_id,scheduled_at). FK restrict para histórico. IDs externos string; tokens encrypted casts. Índices para status/horário, FK e consultas paginadas.
Evolução após testes: media_assets (path, mime, bytes, sha256, validity); captions (body, validity); content_variants (media_id, caption_id, unique pair); campaigns; campaign_groups (unique pair); schedules (timezone, local_time, weekdays). Publications deve preservar snapshots e referências, e revisão humana. Migração expand/contract; não descartar tabela contents até validar backfill e testes. Limites por grupo devem considerar todas as campanhas.

## Migrations aditivas 000002 e 000003
whatsapp_groups: archived/muted/unread_count opcionais, status de anúncio, origem e data do convite, autorização datada, limite diário, tentativa de metadados. instances: sync_state e sync_attempted_at. group_schedules: FK única por grupo, FK content_id, dates locais, interval_days, weekdays e times JSON, enabled. Sem perda do histórico anterior. Consulte RECURRENCE.md. Não é ainda o modelo expandido de campanhas e biblioteca separada.
