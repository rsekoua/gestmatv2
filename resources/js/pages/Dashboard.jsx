import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { Box, Users, ClipboardList, Activity } from 'lucide-react';
import StatCard from '../components/StatCard';

const Dashboard = () => {
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchStats = async () => {
            try {
                const response = await axios.get('/api/v1/dashboard/stats');
                setStats(response.data);
            } catch (error) {
                console.error('Error fetching stats:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchStats();
    }, []);

    if (loading) {
        return <div className="flex items-center justify-center h-full">Loading...</div>;
    }

    return (
        <div className="p-8">
            <h1 className="text-2xl font-bold text-gray-900 mb-8">Dashboard Overview</h1>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <StatCard
                    title="Total Materials"
                    value={stats?.total_materials || 0}
                    icon={Box}
                    color="bg-blue-500"
                />
                <StatCard
                    title="Total Employees"
                    value={stats?.total_employees || 0}
                    icon={Users}
                    color="bg-green-500"
                />
                <StatCard
                    title="Active Attributions"
                    value={stats?.active_attributions || 0}
                    icon={ClipboardList}
                    color="bg-purple-500"
                />
                <StatCard
                    title="Recent Activity"
                    value={stats?.recent_attributions?.length || 0}
                    icon={Activity}
                    color="bg-orange-500"
                />
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div className="p-6 border-b border-gray-100">
                    <h2 className="text-lg font-semibold text-gray-900">Recent Attributions</h2>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full text-left">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th className="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                <th className="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th className="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100">
                            {stats?.recent_attributions?.map((attr) => (
                                <tr key={attr.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 text-sm text-gray-900">
                                        {attr.employee?.nom} {attr.employee?.prenom}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-gray-500">
                                        {attr.materiel?.marque} {attr.materiel?.modele}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-gray-500">
                                        {new Date(attr.created_at).toLocaleDateString()}
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;
