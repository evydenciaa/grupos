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
