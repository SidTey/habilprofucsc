import React, { useState } from 'react';

export default function TestPage() {
    const [count, setCount] = useState(0);

    return (
        <div className="p-6">
            <h2 className="text-2xl font-semibold mb-4">Página de prueba React</h2>
            <p className="mb-4 text-[#706f6c]">Esta es una página de prueba integrada en Laravel + Vite.</p>
            <div className="flex items-center gap-4">
                <button
                    onClick={() => setCount((c) => c + 1)}
                    className="px-4 py-2 bg-[#1b1b18] text-white rounded-sm"
                >
                    Incrementar
                </button>
                <span className="text-sm">Has hecho clic {count} {count === 1 ? 'vez' : 'veces'}</span>
            </div>
        </div>
    );
}
