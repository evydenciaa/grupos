# Z-API
Base fixa https://api.z-api.io/instances/{instanceId}/token/{token}/ ; header Client-Token. HTTPS e sem redirecionar; segredos fora de logs. Consultar contratos atuais antes de alteração.
- GET status: https://developer.z-api.io/instance/status
- GET device: https://developer.z-api.io/instance/device
- GET groups?page=1&pageSize=50: https://developer.z-api.io/group/get-groups
- GET light-group-metadata/{phone}: https://developer.z-api.io/group/light-group-metadata
- GET group-metadata/{phone}: https://developer.z-api.io/group/metadata-group
- GET group-invitation-link/{groupId}: https://developer.z-api.io/group/get-invitation-link
- POST send-image: https://developer.z-api.io/message/send-message-image
- POST send-video: https://developer.z-api.io/message/send-message-video
Os dois POSTs usam phone (ID de grupo), image ou video (URL HTTPS), caption. Retorno com messageId não é confirmação de entrega. Não usar Base64 para vídeos no Plesk. Não inventar links nem coletar participantes. Metadados/convite podem depender de permissão. Validar conta conectada e regras atuais; ausência só após paginação completa. Timeout de POST => unknown, sem retry. Webhooks: validar contrato/autenticidade antes de implementar; eventos idempotentes.

## Contrato revalidado 25/11/2026
/groups usa page/pageSize, isGroup booleano, phone e name strings; unread e lastMessageTime strings (timestamp ms), isMuted 0/1, archived booleano. Metadados leves também possuem participants; conte a lista sem persistir identidades. Full metadata traz invitationLink apenas quando disponível, normalmente admin. O campo phone de /device é a identidade da conta, não o ID do grupo. Controle de estado de sync: running, complete, failed, identity_changed, never. Não interpretar a ausência em uma página incompleta como saída de grupo. Contagem local pode estar desatualizada; mostrar metadata_at e não substituir null por zero.
