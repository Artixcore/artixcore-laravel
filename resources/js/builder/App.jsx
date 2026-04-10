import { useEffect, useState, useMemo } from 'react';
import {
    DndContext,
    PointerSensor,
    closestCenter,
    useSensor,
    useSensors,
} from '@dnd-kit/core';
import { SortableContext, useSortable, verticalListSortingStrategy } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { useBuilderStore } from './store';
import * as api from './api';
import { createBlock, cloneNodeDeep } from './blueprints';

const queryClient = new QueryClient();

function findNode(root, id) {
    if (root.id === id) return root;
    for (const c of root.children || []) {
        const f = findNode(c, id);
        if (f) return f;
    }
    return null;
}

function SortableBlock({ node, depth, onSelect, selectedId, deviceWidth }) {
    const { attributes, listeners, setNodeRef, transform, transition, isDragging } = useSortable({
        id: node.id,
    });
    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
        opacity: isDragging ? 0.5 : 1,
    };
    return (
        <div ref={setNodeRef} style={style} className="mb-2 cursor-grab" {...attributes} {...listeners}>
            <NodePreview
                node={node}
                depth={depth}
                onSelect={onSelect}
                selectedId={selectedId}
                deviceWidth={deviceWidth}
            />
        </div>
    );
}

function NodePreview({ node, depth, onSelect, selectedId, deviceWidth }) {
    const pad = Math.min(depth * 12, 48);
    const selected = node.id === selectedId;
    const base =
        'rounded-lg border transition ' +
        (selected ? 'border-indigo-400 ring-1 ring-indigo-500/40' : 'border-slate-700 hover:border-slate-600');

    const inner = () => {
        switch (node.type) {
            case 'root':
                return (
                    <div className="space-y-2 p-2" style={{ maxWidth: deviceWidth, margin: '0 auto' }}>
                        {node.children?.map((ch) => (
                            <NodePreview
                                key={ch.id}
                                node={ch}
                                depth={depth + 1}
                                onSelect={onSelect}
                                selectedId={selectedId}
                                deviceWidth="100%"
                            />
                        ))}
                    </div>
                );
            case 'section':
                return (
                    <div className="bg-slate-800/50 p-4">
                        <div className="text-xs uppercase tracking-wide text-slate-500">Section</div>
                        {node.children?.map((ch) => (
                            <NodePreview
                                key={ch.id}
                                node={ch}
                                depth={depth + 1}
                                onSelect={onSelect}
                                selectedId={selectedId}
                                deviceWidth="100%"
                            />
                        ))}
                    </div>
                );
            case 'columns':
                return (
                    <div className="grid grid-cols-2 gap-3">
                        {node.children?.map((col) => (
                            <div key={col.id} className="rounded border border-slate-600/80 bg-slate-900/40 p-2">
                                <div className="text-xs text-slate-500">Column</div>
                                {col.children?.map((ch) => (
                                    <NodePreview
                                        key={ch.id}
                                        node={ch}
                                        depth={depth + 1}
                                        onSelect={onSelect}
                                        selectedId={selectedId}
                                        deviceWidth="100%"
                                    />
                                ))}
                            </div>
                        ))}
                    </div>
                );
            case 'hero':
                return (
                    <div className="space-y-2 py-6 text-center">
                        <p className="text-xs font-medium text-indigo-300">{node.props?.eyebrow}</p>
                        <h2 className="text-2xl font-semibold text-white">{node.props?.title}</h2>
                        <p className="text-slate-300">{node.props?.subtitle}</p>
                    </div>
                );
            case 'feature_grid':
                return (
                    <div className="py-4">
                        <h3 className="mb-3 text-lg font-medium text-white">{node.props?.heading}</h3>
                        <ul className="grid gap-3 sm:grid-cols-3">
                            {(node.props?.items || []).map((it, i) => (
                                <li key={i} className="rounded-lg bg-slate-900/60 p-3 text-sm">
                                    <div className="font-medium text-white">{it.title}</div>
                                    <div className="text-slate-400">{it.description}</div>
                                </li>
                            ))}
                        </ul>
                    </div>
                );
            case 'cta':
                return (
                    <div className="rounded-xl bg-indigo-950/50 p-6 text-center">
                        <h3 className="text-xl font-semibold text-white">{node.props?.title}</h3>
                        <p className="mt-2 text-slate-300">{node.props?.body}</p>
                        <span className="mt-4 inline-block rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium">
                            {node.props?.buttonLabel}
                        </span>
                    </div>
                );
            case 'rich_text':
                return (
                    <div
                        className="prose prose-invert max-w-none text-sm"
                        dangerouslySetInnerHTML={{ __html: node.props?.html || '' }}
                    />
                );
            case 'heading':
                return <div className="text-xl font-semibold text-white">{node.props?.text}</div>;
            case 'paragraph':
                return <p className="text-slate-300">{node.props?.text}</p>;
            case 'divider':
                return <hr className="border-slate-600" />;
            case 'spacer':
                return <div className="h-8" />;
            default:
                return <div className="text-slate-500">[{node.type}]</div>;
        }
    };

    return (
        <div
            className={base}
            style={{ marginLeft: pad }}
            onClick={(e) => {
                e.stopPropagation();
                onSelect(node.id);
            }}
            role="presentation"
        >
            {inner()}
        </div>
    );
}

function Inspector() {
    const document = useBuilderStore((s) => s.document);
    const selectedId = useBuilderStore((s) => s.selectedId);
    const updateNode = useBuilderStore((s) => s.updateNode);

    const node = selectedId ? findNode(document.root, selectedId) : null;
    if (!node) {
        return <p className="text-sm text-slate-500">Select a block to edit properties.</p>;
    }

    const setProp = (key, value) => {
        updateNode(node.id, (n) => ({
            ...n,
            props: { ...n.props, [key]: value },
        }));
    };

    const fieldsFor = () => {
        switch (node.type) {
            case 'hero':
                return (
                    <>
                        <Field label="Eyebrow" value={node.props?.eyebrow || ''} onChange={(v) => setProp('eyebrow', v)} />
                        <Field label="Title" value={node.props?.title || ''} onChange={(v) => setProp('title', v)} />
                        <Field label="Subtitle" value={node.props?.subtitle || ''} onChange={(v) => setProp('subtitle', v)} />
                        <Field
                            label="Primary CTA label"
                            value={node.props?.primaryCta?.label || ''}
                            onChange={(v) =>
                                setProp('primaryCta', { ...node.props.primaryCta, label: v })
                            }
                        />
                        <Field
                            label="Primary CTA href"
                            value={node.props?.primaryCta?.href || ''}
                            onChange={(v) =>
                                setProp('primaryCta', { ...node.props.primaryCta, href: v })
                            }
                        />
                    </>
                );
            case 'cta':
                return (
                    <>
                        <Field label="Title" value={node.props?.title || ''} onChange={(v) => setProp('title', v)} />
                        <Field label="Body" value={node.props?.body || ''} onChange={(v) => setProp('body', v)} />
                        <Field
                            label="Button"
                            value={node.props?.buttonLabel || ''}
                            onChange={(v) => setProp('buttonLabel', v)}
                        />
                        <Field label="Href" value={node.props?.href || ''} onChange={(v) => setProp('href', v)} />
                    </>
                );
            case 'feature_grid':
                return (
                    <>
                        <Field label="Heading" value={node.props?.heading || ''} onChange={(v) => setProp('heading', v)} />
                        <label className="mt-2 block text-xs text-slate-400">Items (JSON)</label>
                        <textarea
                            className="mt-1 w-full rounded border border-slate-600 bg-slate-900 px-2 py-1 font-mono text-xs text-slate-200"
                            rows={8}
                            value={JSON.stringify(node.props?.items || [], null, 2)}
                            onChange={(e) => {
                                try {
                                    setProp('items', JSON.parse(e.target.value));
                                } catch {
                                    /* ignore */
                                }
                            }}
                        />
                    </>
                );
            case 'rich_text':
                return (
                    <>
                        <label className="text-xs text-slate-400">HTML</label>
                        <textarea
                            className="mt-1 w-full rounded border border-slate-600 bg-slate-900 px-2 py-1 font-mono text-xs text-slate-200"
                            rows={10}
                            value={node.props?.html || ''}
                            onChange={(e) => setProp('html', e.target.value)}
                        />
                    </>
                );
            case 'heading':
                return (
                    <>
                        <Field label="Text" value={node.props?.text || ''} onChange={(v) => setProp('text', v)} />
                        <label className="mt-2 block text-xs text-slate-400">Level</label>
                        <select
                            className="mt-1 w-full rounded border border-slate-600 bg-slate-900 px-2 py-1 text-sm"
                            value={node.props?.level || 2}
                            onChange={(e) => setProp('level', Number(e.target.value))}
                        >
                            {[1, 2, 3, 4].map((n) => (
                                <option key={n} value={n}>
                                    H{n}
                                </option>
                            ))}
                        </select>
                    </>
                );
            case 'paragraph':
                return <Field label="Text" value={node.props?.text || ''} onChange={(v) => setProp('text', v)} />;
            default:
                return (
                    <pre className="max-h-64 overflow-auto rounded bg-slate-900 p-2 text-xs text-slate-300">
                        {JSON.stringify(node.props, null, 2)}
                    </pre>
                );
        }
    };

    return (
        <div className="space-y-3">
            <p className="text-xs text-slate-500">
                {node.type} · {node.id.slice(0, 8)}…
            </p>
            {fieldsFor()}
        </div>
    );
}

function Field({ label, value, onChange }) {
    return (
        <label className="block">
            <span className="text-xs text-slate-400">{label}</span>
            <input
                className="mt-1 w-full rounded border border-slate-600 bg-slate-900 px-2 py-1 text-sm text-white"
                value={value}
                onChange={(e) => onChange(e.target.value)}
            />
        </label>
    );
}

function BuilderShell({ pageId }) {
    const [templates, setTemplates] = useState([]);
    const [status, setStatus] = useState('');
    const [err, setErr] = useState('');

    const bootstrapFromApi = useBuilderStore((s) => s.bootstrapFromApi);
    const document = useBuilderStore((s) => s.document);
    const latestVersionId = useBuilderStore((s) => s.latestVersionId);
    const setLatestVersionId = useBuilderStore((s) => s.setLatestVersionId);
    const setDocument = useBuilderStore((s) => s.setDocument);
    const appendToRoot = useBuilderStore((s) => s.appendToRoot);
    const reorderRootChildren = useBuilderStore((s) => s.reorderRootChildren);
    const selectedId = useBuilderStore((s) => s.selectedId);
    const setSelectedId = useBuilderStore((s) => s.setSelectedId);
    const device = useBuilderStore((s) => s.device);
    const setDevice = useBuilderStore((s) => s.setDevice);
    const undo = useBuilderStore((s) => s.undo);
    const redo = useBuilderStore((s) => s.redo);
    const saving = useBuilderStore((s) => s.saving);
    const setSaving = useBuilderStore((s) => s.setSaving);
    const deleteNode = useBuilderStore((s) => s.deleteNode);
    const aiOpen = useBuilderStore((s) => s.aiOpen);
    const setAiOpen = useBuilderStore((s) => s.setAiOpen);
    const pendingAiDocument = useBuilderStore((s) => s.pendingAiDocument);
    const pendingAiRationale = useBuilderStore((s) => s.pendingAiRationale);
    const setPendingAi = useBuilderStore((s) => s.setPendingAi);
    const acceptPendingAi = useBuilderStore((s) => s.acceptPendingAi);
    const rejectPendingAi = useBuilderStore((s) => s.rejectPendingAi);

    const [aiPrompt, setAiPrompt] = useState('');
    const [aiLoading, setAiLoading] = useState(false);

    const sensors = useSensors(useSensor(PointerSensor, { activationConstraint: { distance: 6 } }));

    const deviceWidth = useMemo(() => {
        if (device === 'mobile') return '390px';
        if (device === 'tablet') return '768px';
        return '100%';
    }, [device]);

    useEffect(() => {
        (async () => {
            try {
                const data = await api.fetchPage(pageId);
                bootstrapFromApi(data);
                const t = await api.listTemplates();
                setTemplates(t);
            } catch (e) {
                setErr(e?.response?.data?.message || 'Failed to load builder.');
            }
        })();
    }, [pageId, bootstrapFromApi]);

    const rootChildIds = (document.root.children || []).map((c) => c.id);

    const onDragEnd = (event) => {
        const { active, over } = event;
        if (!over || active.id === over.id) return;
        reorderRootChildren(String(active.id), String(over.id));
    };

    const handleSave = async () => {
        setSaving(true);
        setErr('');
        try {
            const res = await api.saveDocument(pageId, document, latestVersionId);
            setLatestVersionId(res.latest_version_id);
            setStatus('Saved');
            setTimeout(() => setStatus(''), 2000);
        } catch (e) {
            if (e.response?.status === 409) {
                setErr('Conflict: reload the page.');
            } else {
                setErr(e?.response?.data?.message || 'Save failed');
            }
        } finally {
            setSaving(false);
        }
    };

    const handlePublish = async () => {
        if (!confirm('Publish this page to the live API?')) return;
        try {
            await api.publishPage(pageId);
            setStatus('Published');
        } catch (e) {
            setErr(e?.response?.data?.message || 'Publish failed (check builder.publish permission).');
        }
    };

    const handleTemplate = async (slug) => {
        try {
            const res = await api.applyTemplate(pageId, slug);
            setLatestVersionId(res.latest_version_id);
            setDocument(res.document, false);
            setStatus('Template applied');
        } catch (e) {
            setErr(e?.response?.data?.message || 'Template failed');
        }
    };

    const handleAi = async () => {
        setAiLoading(true);
        setErr('');
        try {
            const res = await api.aiPropose(pageId, aiPrompt, document, selectedId);
            setPendingAi(res.document, res.rationale);
        } catch (e) {
            setErr(e?.response?.data?.message || 'AI request failed');
        } finally {
            setAiLoading(false);
        }
    };

    const palette = [
        ['section', 'Section'],
        ['columns', '2 columns'],
        ['hero', 'Hero'],
        ['feature_grid', 'Features'],
        ['cta', 'CTA'],
        ['heading', 'Heading'],
        ['paragraph', 'Paragraph'],
        ['rich_text', 'Rich text'],
        ['divider', 'Divider'],
        ['spacer', 'Spacer'],
    ];

    return (
        <div className="flex h-screen flex-col bg-slate-950 text-slate-100">
            <header className="flex flex-wrap items-center gap-3 border-b border-slate-800 px-4 py-3">
                <h1 className="text-sm font-semibold text-white">Page builder</h1>
                <div className="flex gap-1 rounded-lg bg-slate-800 p-1">
                    {['desktop', 'tablet', 'mobile'].map((d) => (
                        <button
                            key={d}
                            type="button"
                            onClick={() => setDevice(d)}
                            className={
                                'rounded px-2 py-1 text-xs capitalize ' +
                                (device === d ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white')
                            }
                        >
                            {d}
                        </button>
                    ))}
                </div>
                <button
                    type="button"
                    onClick={undo}
                    className="rounded border border-slate-600 px-2 py-1 text-xs hover:bg-slate-800"
                >
                    Undo
                </button>
                <button
                    type="button"
                    onClick={redo}
                    className="rounded border border-slate-600 px-2 py-1 text-xs hover:bg-slate-800"
                >
                    Redo
                </button>
                <button
                    type="button"
                    onClick={handleSave}
                    disabled={saving}
                    className="rounded bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-500 disabled:opacity-50"
                >
                    {saving ? 'Saving…' : 'Save'}
                </button>
                <button
                    type="button"
                    onClick={handlePublish}
                    className="rounded border border-emerald-700 px-3 py-1.5 text-xs font-medium text-emerald-300 hover:bg-emerald-950"
                >
                    Publish
                </button>
                <button
                    type="button"
                    onClick={() => setAiOpen(!aiOpen)}
                    className="rounded border border-violet-700 px-3 py-1.5 text-xs font-medium text-violet-200 hover:bg-violet-950"
                >
                    AI
                </button>
                {status && <span className="text-xs text-emerald-400">{status}</span>}
                {err && <span className="text-xs text-red-400">{err}</span>}
            </header>
            <div className="flex min-h-0 flex-1">
                <aside className="w-56 shrink-0 overflow-y-auto border-r border-slate-800 p-3">
                    <p className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Blocks</p>
                    <div className="flex flex-col gap-1">
                        {palette.map(([type, label]) => (
                            <button
                                key={type}
                                type="button"
                                onClick={() => appendToRoot(createBlock(type))}
                                className="rounded border border-slate-700 bg-slate-900 px-2 py-1.5 text-left text-xs hover:border-indigo-500"
                            >
                                {label}
                            </button>
                        ))}
                    </div>
                    <p className="mb-2 mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Templates</p>
                    <div className="flex flex-col gap-1">
                        {templates.map((t) => (
                            <button
                                key={t.slug}
                                type="button"
                                onClick={() => handleTemplate(t.slug)}
                                className="rounded border border-slate-700 px-2 py-1.5 text-left text-xs hover:border-indigo-500"
                            >
                                {t.name}
                            </button>
                        ))}
                    </div>
                </aside>
                <main className="min-w-0 flex-1 overflow-y-auto bg-slate-900/40 p-4">
                    <div className="mx-auto min-h-full rounded-xl border border-slate-800 bg-slate-950 p-4 shadow-xl">
                        <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={onDragEnd}>
                            <SortableContext items={rootChildIds} strategy={verticalListSortingStrategy}>
                                <div style={{ maxWidth: deviceWidth, margin: '0 auto' }}>
                                    {(document.root.children || []).map((node) => (
                                        <SortableBlock
                                            key={node.id}
                                            node={node}
                                            depth={0}
                                            onSelect={setSelectedId}
                                            selectedId={selectedId}
                                            deviceWidth="100%"
                                        />
                                    ))}
                                </div>
                            </SortableContext>
                        </DndContext>
                        {rootChildIds.length === 0 && (
                            <p className="py-20 text-center text-sm text-slate-500">
                                Add blocks from the left palette or apply a template.
                            </p>
                        )}
                    </div>
                </main>
                <aside className="w-80 shrink-0 overflow-y-auto border-l border-slate-800 p-3">
                    <p className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Properties</p>
                    <Inspector />
                    {selectedId && (
                        <div className="mt-4 flex flex-col gap-2 border-t border-slate-800 pt-4">
                            <button
                                type="button"
                                onClick={() => {
                                    const n = findNode(document.root, selectedId);
                                    if (n) appendToRoot(cloneNodeDeep(n));
                                }}
                                className="rounded border border-slate-600 px-2 py-1 text-xs"
                            >
                                Duplicate
                            </button>
                            <button
                                type="button"
                                onClick={() => deleteNode(selectedId)}
                                className="rounded border border-red-900 px-2 py-1 text-xs text-red-300"
                            >
                                Delete
                            </button>
                        </div>
                    )}
                </aside>
                {aiOpen && (
                    <div className="fixed bottom-4 right-4 z-50 w-96 max-w-[calc(100vw-2rem)] rounded-xl border border-violet-800 bg-slate-900 p-4 shadow-2xl">
                        <p className="text-sm font-semibold text-violet-200">Agentic builder</p>
                        <textarea
                            className="mt-2 w-full rounded border border-slate-600 bg-slate-950 px-2 py-2 text-sm"
                            rows={4}
                            placeholder="Describe the page or section you want…"
                            value={aiPrompt}
                            onChange={(e) => setAiPrompt(e.target.value)}
                        />
                        <p className="mt-1 text-xs text-slate-500">
                            Optional: select a block first to focus edits on that subtree.
                        </p>
                        <button
                            type="button"
                            disabled={aiLoading}
                            onClick={handleAi}
                            className="mt-2 w-full rounded bg-violet-600 py-2 text-sm font-medium text-white hover:bg-violet-500 disabled:opacity-50"
                        >
                            {aiLoading ? 'Generating…' : 'Propose'}
                        </button>
                        {pendingAiRationale && (
                            <div className="mt-3 rounded border border-slate-700 bg-slate-950 p-2 text-xs text-slate-300">
                                <p className="font-medium text-slate-200">Suggestion</p>
                                <p className="mt-1">{pendingAiRationale}</p>
                                <div className="mt-2 flex gap-2">
                                    <button
                                        type="button"
                                        onClick={acceptPendingAi}
                                        className="flex-1 rounded bg-emerald-700 py-1 text-xs text-white"
                                    >
                                        Apply to canvas
                                    </button>
                                    <button
                                        type="button"
                                        onClick={rejectPendingAi}
                                        className="flex-1 rounded border border-slate-600 py-1 text-xs"
                                    >
                                        Dismiss
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}

export default function App({ pageId }) {
    return (
        <QueryClientProvider client={queryClient}>
            <BuilderShell pageId={pageId} />
        </QueryClientProvider>
    );
}
