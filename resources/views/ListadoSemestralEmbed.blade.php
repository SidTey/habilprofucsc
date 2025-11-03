<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado Semestral</title>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="iframe-embed">
    <div id="app"></div>
    <script type="module">
        import React from 'react';
        import { createRoot } from 'react-dom/client';
        import ListadoSemestral from './resources/js/components/ListadoSemestral.jsx';
        
        const container = document.getElementById('app');
        const root = createRoot(container);
        root.render(React.createElement(ListadoSemestral));
    </script>
</body>
</html>
