/**
 * CSRF Token helper
 */
export function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.getAttribute('content') : '';
}

export function addCsrfToFormData(formData) {
  const token = getCsrfToken();
  if (token) {
    formData.append('csrf_token', token);
  }
  return formData;
}
