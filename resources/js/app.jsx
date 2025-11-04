import React from 'react';
import { createRoot } from 'react-dom/client';
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css'; 
import HabilprofForm from './components/HabilprofForm.jsx';
import ListadoHistorico from './components/ListadoHistorico.jsx';
import ListadoSemestral from './components/ListadoSemestral.jsx';

function App() {
    // Detectar qué componente renderizar basado en la URL o elemento
    const path = window.location.pathname;
    
    if (path.includes('historico-embed')) {
        return <ListadoHistorico />;
    }
    
    if (path.includes('semestral-embed')) {
        return <ListadoSemestral />;
    }
    
    // Por defecto, renderizar el formulario de ingreso
    return (
        <div className="min-vh-100 d-flex align-items-center justify-content-center bg-light">            
            <HabilprofForm />
        </div>
    );
}

const el = document.getElementById('app');
if (el) {
    createRoot(el).render(<App />);
}