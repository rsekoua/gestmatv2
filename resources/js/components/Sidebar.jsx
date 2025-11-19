import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { LayoutDashboard, Box, Users, Settings, LogOut } from 'lucide-react';
import clsx from 'clsx';

const Sidebar = () => {
    const location = useLocation();

    const navItems = [
        { name: 'Dashboard', path: '/app', icon: LayoutDashboard },
        { name: 'Materials', path: '/app/materials', icon: Box },
        { name: 'Employees', path: '/app/employees', icon: Users },
        { name: 'Settings', path: '/app/settings', icon: Settings },
    ];

    return (
        <div className="h-screen w-64 bg-white border-r border-gray-200 flex flex-col">
            <div className="p-6">
                <h1 className="text-2xl font-bold text-indigo-600">GestMat</h1>
            </div>

            <nav className="flex-1 px-4 space-y-2">
                {navItems.map((item) => {
                    const Icon = item.icon;
                    const isActive = location.pathname === item.path;
                    return (
                        <Link
                            key={item.path}
                            to={item.path}
                            className={clsx(
                                'flex items-center px-4 py-3 rounded-lg transition-colors duration-200',
                                isActive
                                    ? 'bg-indigo-50 text-indigo-600'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                            )}
                        >
                            <Icon className="w-5 h-5 mr-3" />
                            <span className="font-medium">{item.name}</span>
                        </Link>
                    );
                })}
            </nav>

            <div className="p-4 border-t border-gray-200">
                <button className="flex items-center w-full px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors duration-200">
                    <LogOut className="w-5 h-5 mr-3" />
                    <span className="font-medium">Logout</span>
                </button>
            </div>
        </div>
    );
};

export default Sidebar;
