/**
 * Placeholder for Sanctum SPA auth. When login is implemented:
 * 1. GET /sanctum/csrf-cookie (credentials: include)
 * 2. POST /login with session
 * 3. Use apiFetch with credentials: "include" and X-XSRF-TOKEN from cookies
 */

export type AuthUser = {
  id: number;
  name: string;
  email: string;
};

export async function getCurrentUser(): Promise<AuthUser | null> {
  return null;
}
