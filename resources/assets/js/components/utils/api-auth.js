/**
 * Utilitats d'autenticació API en client.
 *
 * Fase de transició: prioritza meta `user-token` i manté fallback legacy
 * per no trencar pantalles encara no migrades.
 */
export function getApiToken() {
  const metaToken = document.head.querySelector('meta[name="user-token"]');
  if (metaToken && metaToken.content) {
    return metaToken.content;
  }

  const legacyTokenNode = document.getElementById('_token');
  if (legacyTokenNode && legacyTokenNode.innerHTML) {
    return legacyTokenNode.innerHTML;
  }

  return '';
}

/**
 * Injecta Authorization Bearer quan hi ha token disponible.
 */
export function withApiAuth(config = {}) {
  const token = getApiToken();
  if (!token) {
    return config;
  }

  const headers = { ...(config.headers || {}), Authorization: `Bearer ${token}` };

  return {
    ...config,
    headers,
  };
}
