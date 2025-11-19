import './bootstrap';
import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import Sidebar from './components/Sidebar';
import Dashboard from './pages/Dashboard';
import Materials from './pages/Materials';
import Employees from './pages/Employees';

function App() {
    return (
        <BrowserRouter>
            <div className="flex min-h-screen bg-gray-50">
                <Sidebar />
                <main className="flex-1 overflow-y-auto">
                    <Routes>
                        <Route path="/app" element={<Dashboard />} />
                        <Route path="/app/materials" element={<Materials />} />
                        <Route path="/app/employees" element={<Employees />} />
                        <Route path="/app/settings" element={<div className="p-8">Settings Page (Coming Soon)</div>} />
                        <Route path="*" element={<Navigate to="/app" replace />} />
                    </Routes>
                </main>
            </div>
        </BrowserRouter>
    );
}

const root = ReactDOM.createRoot(document.getElementById('app'));
root.render(
    <React.StrictMode>
        <App />
    </React.StrictMode>
);
