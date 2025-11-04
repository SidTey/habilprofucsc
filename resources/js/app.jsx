import './bootstrap';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import App from './components/App';

// Asegurarse que el DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {
    const rootElement = document.getElementById('app');
    if (rootElement) {
        const root = createRoot(rootElement);
        root.render(
            <StrictMode>
                <App />
            </StrictMode>
        );
    } else {
        console.error('No se encontró el elemento #app');
    }
});
