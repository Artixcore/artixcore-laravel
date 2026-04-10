import { createRoot } from 'react-dom/client';
import App from './App.jsx';

const el = document.getElementById('builder-root');
if (el) {
    const pageId = Number(el.dataset.pageId);
    createRoot(el).render(<App pageId={pageId} />);
}
