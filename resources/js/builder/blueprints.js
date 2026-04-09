export function uuid() {
    return crypto.randomUUID();
}

export function emptyRoot() {
    return {
        id: uuid(),
        type: 'root',
        version: 1,
        props: {},
        children: [],
        responsive: null,
        visibility: null,
    };
}

export function createBlock(type) {
    const id = uuid();
    switch (type) {
        case 'section':
            return {
                id,
                type: 'section',
                version: 1,
                props: { paddingY: 'md' },
                children: [],
                responsive: null,
                visibility: null,
            };
        case 'hero':
            return {
                id,
                type: 'hero',
                version: 1,
                props: {
                    eyebrow: '',
                    title: 'New headline',
                    subtitle: 'Supporting line for your offer.',
                    primaryCta: { label: 'Primary', href: '/contact' },
                    secondaryCta: { label: 'Secondary', href: '#' },
                },
                children: [],
                responsive: null,
                visibility: null,
            };
        case 'feature_grid':
            return {
                id,
                type: 'feature_grid',
                version: 1,
                props: {
                    heading: 'Features',
                    items: [
                        { title: 'Item one', description: 'Description', href: '#' },
                        { title: 'Item two', description: 'Description', href: '#' },
                    ],
                },
                children: [],
                responsive: null,
                visibility: null,
            };
        case 'cta':
            return {
                id,
                type: 'cta',
                version: 1,
                props: {
                    title: 'Call to action',
                    body: 'Short supporting copy.',
                    buttonLabel: 'Get started',
                    href: '/contact',
                },
                children: [],
                responsive: null,
                visibility: null,
            };
        case 'rich_text':
            return {
                id,
                type: 'rich_text',
                version: 1,
                props: { html: '<p>Rich text</p>' },
                children: [],
                responsive: null,
                visibility: null,
            };
        case 'heading':
            return {
                id,
                type: 'heading',
                version: 1,
                props: { text: 'Heading', level: 2 },
                children: [],
                responsive: null,
                visibility: null,
            };
        case 'paragraph':
            return {
                id,
                type: 'paragraph',
                version: 1,
                props: { text: 'Paragraph text.' },
                children: [],
                responsive: null,
                visibility: null,
            };
        case 'columns':
            return {
                id,
                type: 'columns',
                version: 1,
                props: { gap: 'md' },
                children: [
                    {
                        id: uuid(),
                        type: 'column',
                        version: 1,
                        props: { span: 6 },
                        children: [],
                        responsive: null,
                        visibility: null,
                    },
                    {
                        id: uuid(),
                        type: 'column',
                        version: 1,
                        props: { span: 6 },
                        children: [],
                        responsive: null,
                        visibility: null,
                    },
                ],
                responsive: null,
                visibility: null,
            };
        case 'divider':
            return {
                id,
                type: 'divider',
                version: 1,
                props: {},
                children: [],
                responsive: null,
                visibility: null,
            };
        case 'spacer':
            return {
                id,
                type: 'spacer',
                version: 1,
                props: { height: 'md' },
                children: [],
                responsive: null,
                visibility: null,
            };
        default:
            return createBlock('paragraph');
    }
}

export function cloneNodeDeep(node) {
    const walk = (n) => {
        const copy = {
            ...n,
            id: uuid(),
            props: n.props ? { ...n.props } : {},
            children: Array.isArray(n.children) ? n.children.map((c) => walk(c)) : [],
        };
        if (copy.props.items && Array.isArray(copy.props.items)) {
            copy.props.items = copy.props.items.map((i) => ({ ...i }));
        }
        if (copy.props.primaryCta) copy.props.primaryCta = { ...copy.props.primaryCta };
        if (copy.props.secondaryCta) copy.props.secondaryCta = { ...copy.props.secondaryCta };
        return copy;
    };
    return walk(node);
}
