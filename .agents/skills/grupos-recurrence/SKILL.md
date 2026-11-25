---
name: grupos-recurrence
description: "Auditar e desenvolver a agenda editorial por grupo, datas, horários, recorrência e revisão."
---
Leia docs/RECURRENCE.md, docs/AUDIT-2026-11-25.md e RecurrenceCalendar. Preserve UTC e America/Sao_Paulo; valide intervalo, dias ISO, validade e limites autorizados. Use testes determinísticos sem rede. Não transformar a agenda em envio automático sem homologação e autorização específicas; não recuperar horários vencidos. Priorize experiência Filament e cron leve para manutenção. Registre resultados reais em STATUS.
