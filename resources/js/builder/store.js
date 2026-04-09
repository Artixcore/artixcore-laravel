import { create } from 'zustand';
import { emptyRoot } from './blueprints';

const MAX_UNDO = 40;

export const useBuilderStore = create((set, get) => ({
    pageMeta: null,
    document: { schemaVersion: 1, root: emptyRoot() },
    latestVersionId: null,
    selectedId: null,
    device: 'desktop',
    past: [],
    future: [],
    saving: false,
    aiOpen: false,
    pendingAiDocument: null,
    pendingAiRationale: null,

    bootstrapFromApi(payload) {
        set({
            pageMeta: payload.page,
            document: payload.document,
            latestVersionId: payload.latest_version_id,
            selectedId: null,
            past: [],
            future: [],
        });
    },

    setSelectedId(id) {
        set({ selectedId: id });
    },

    setDevice(device) {
        set({ device });
    },

    setDocument(doc, pushHistory = true) {
        const prev = get().document;
        if (pushHistory && prev) {
            const past = [...get().past, JSON.stringify(prev)];
            if (past.length > MAX_UNDO) past.shift();
            set({ past, future: [], document: doc });
        } else {
            set({ document: doc });
        }
    },

    undo() {
        const { past, document, future } = get();
        if (past.length === 0) return;
        const prev = past[past.length - 1];
        set({
            past: past.slice(0, -1),
            future: [JSON.stringify(document), ...future],
            document: JSON.parse(prev),
        });
    },

    redo() {
        const { future, document, past } = get();
        if (future.length === 0) return;
        const next = future[0];
        set({
            future: future.slice(1),
            past: [...past, JSON.stringify(document)],
            document: JSON.parse(next),
        });
    },

    updateNode(nodeId, updater) {
        const doc = structuredClone(get().document);
        const walk = (node) => {
            if (node.id === nodeId) {
                return updater(node);
            }
            if (Array.isArray(node.children)) {
                return { ...node, children: node.children.map(walk) };
            }
            return node;
        };
        doc.root = walk(doc.root);
        get().setDocument(doc);
    },

    deleteNode(nodeId) {
        if (nodeId === get().document.root.id) return;
        const doc = structuredClone(get().document);
        const remove = (node) => {
            if (!Array.isArray(node.children)) return node;
            const next = node.children.filter((c) => c.id !== nodeId).map(remove);
            return { ...node, children: next };
        };
        doc.root = remove(doc.root);
        set({ selectedId: null });
        get().setDocument(doc);
    },

    appendToRoot(block) {
        const doc = structuredClone(get().document);
        doc.root.children = [...(doc.root.children || []), block];
        get().setDocument(doc);
    },

    reorderRootChildren(activeId, overId) {
        const doc = structuredClone(get().document);
        const items = [...(doc.root.children || [])];
        const oldIndex = items.findIndex((c) => c.id === activeId);
        const newIndex = items.findIndex((c) => c.id === overId);
        if (oldIndex < 0 || newIndex < 0) return;
        const [removed] = items.splice(oldIndex, 1);
        items.splice(newIndex, 0, removed);
        doc.root.children = items;
        get().setDocument(doc);
    },

    setLatestVersionId(id) {
        set({ latestVersionId: id });
    },

    setSaving(v) {
        set({ saving: v });
    },

    setAiOpen(v) {
        set({ aiOpen: v });
    },

    setPendingAi(doc, rationale) {
        set({ pendingAiDocument: doc, pendingAiRationale: rationale });
    },

    acceptPendingAi() {
        const d = get().pendingAiDocument;
        if (d) get().setDocument(d);
        set({ pendingAiDocument: null, pendingAiRationale: null });
    },

    rejectPendingAi() {
        set({ pendingAiDocument: null, pendingAiRationale: null });
    },
}));
