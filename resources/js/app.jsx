import React from 'react';
import { createRoot } from 'react-dom/client';
import './bootstrap';
import TestPage from './pages/TestPage';

function App() {
    return (
        <div className="min-h-screen flex items-center justify-center">
            <TestPage />
        </div>
    );
}

const el = document.getElementById('app');
if (el) {
    createRoot(el).render(<App />);
}
