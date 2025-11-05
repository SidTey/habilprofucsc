import React from 'react';

const Dashboard = ({ user }) => {
    return (
        <div style={{ padding: '20px' }}>
            <h3>Bienvenido al Panel de Control.</h3>
            <p>Has iniciado sesión como: **{user.type.toUpperCase()}**</p>
            <pre style={{ backgroundColor: '#eee', padding: '10px' }}>
                {JSON.stringify(user, null, 2)}
            </pre>
            {/* Aquí irá el resto de la interfaz de la aplicación */}
        </div>
    );
};

export default Dashboard;
