export class CajeerClient {
  constructor(baseUrl, token) {
    this.baseUrl = baseUrl.replace(/\/$/, "");
    this.token = token;
  }
  async request(method, path, { query = null, json = null } = {}) {
    const url = new URL(this.baseUrl + path);
    if (query) Object.entries(query).forEach(([k,v]) => url.searchParams.set(k, v));
    const res = await fetch(url.toString(), {
      method,
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${this.token}`,
        ...(json ? { "Content-Type": "application/json" } : {})
      },
      body: json ? JSON.stringify(json) : undefined
    });
    const text = await res.text();
    let data = null;
    try { data = JSON.parse(text); } catch {}
    return { ok: res.ok, status: res.status, data, raw: text };
  }
  contentV1() { return new ContentApi(this); }
  adminV1() { return new AdminApi(this); }
}
export class ContentApi {
  constructor(c) { this.c = c; }
  listNews(query = {}) { return this.c.request("GET", "/api/v1/content/news", { query }); }
}
export class AdminApi {
  constructor(c) { this.c = c; }
  me() { return this.c.request("GET", "/api/v1/admin/me"); }
}
