/**
 * Utilitats d'autenticació API en client.
 *
 * Mode Bearer: usa únicament el token de sessió Sanctum exposat en meta.
 */
export function getApiToken() {
  const metaToken = document.head.querySelector('meta[name="user-bearer-token"]');
  if (metaToken && metaToken.content) {
    return metaToken.content;
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
