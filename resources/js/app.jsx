import React from 'react';
import { createRoot } from 'react-dom/client';
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css'; 
import HabilprofForm from './components/HabilprofForm.jsx'; 

function App() {
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