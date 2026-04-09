import axios from 'axios';

const csrf = document.querySelector('meta[name="csrf-token"]');
if (csrf?.content) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf.content;
}
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true;

const base = '/builder-api/v1';

export async function fetchPage(pageId) {
    const { data } = await axios.get(`${base}/pages/${pageId}`);
    return data.data;
}

export async function saveDocument(pageId, document, baseVersionId, label = 'autosave') {
    const { data } = await axios.put(`${base}/pages/${pageId}/document`, {
        document,
        base_version_id: baseVersionId,
        label,
    });
    return data.data;
}

export async function listVersions(pageId) {
    const { data } = await axios.get(`${base}/pages/${pageId}/versions`);
    return data.data;
}

export async function restoreVersion(pageId, versionId) {
    const { data } = await axios.post(`${base}/pages/${pageId}/versions/${versionId}/restore`);
    return data.data;
}

export async function publishPage(pageId, scheduledAt = null) {
    const { data } = await axios.post(`${base}/pages/${pageId}/publish`, {
        scheduled_at: scheduledAt,
    });
    return data.data;
}

export async function listTemplates() {
    const { data } = await axios.get(`${base}/templates`);
    return data.data;
}

export async function applyTemplate(pageId, slug) {
    const { data } = await axios.post(`${base}/pages/${pageId}/apply-template`, { slug });
    return data.data;
}

export async function exportPage(pageId) {
    const { data } = await axios.get(`${base}/pages/${pageId}/export`);
    return data.data;
}

export async function importPage(pageId, document) {
    const { data } = await axios.post(`${base}/pages/${pageId}/import`, { document });
    return data.data;
}

export async function aiPropose(pageId, prompt, document, targetNodeId) {
    const { data } = await axios.post(`${base}/pages/${pageId}/ai/propose`, {
        prompt,
        document,
        target_node_id: targetNodeId || null,
    });
    return data.data;
}

export async function listSavedSections() {
    const { data } = await axios.get(`${base}/saved-sections`);
    return data.data;
}

export async function saveSection(name, document) {
    const { data } = await axios.post(`${base}/saved-sections`, { name, document });
    return data.data;
}
