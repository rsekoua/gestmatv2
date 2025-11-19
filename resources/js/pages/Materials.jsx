import React, { useEffect, useState } from 'react';
import axios from 'axios';
import DataTable from '../components/DataTable';
import { Plus } from 'lucide-react';

const Materials = () => {
    const [data, setData] = useState([]);
    const [pagination, setPagination] = useState(null);
    const [loading, setLoading] = useState(true);

    const fetchData = async (page = 1) => {
        setLoading(true);
        try {
            const response = await axios.get(`/api/v1/materials?page=${page}`);
            setData(response.data.data);
            setPagination({
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                from: response.data.from,
                to: response.data.to,
                total: response.data.total,
                prev_page_url: response.data.prev_page_url,
                next_page_url: response.data.next_page_url,
            });
        } catch (error) {
            console.error('Error fetching materials:', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchData();
    }, []);

    const columns = [
        { header: 'Marque', accessor: 'marque' },
        { header: 'Modèle', accessor: 'modele' },
        { header: 'S/N', accessor: 'numero_serie' },
        { 
            header: 'Type', 
            accessor: 'type',
            render: (row) => row.materiel_type?.nom || 'N/A'
        },
        { 
            header: 'Statut', 
            accessor: 'statut',
            render: (row) => (
                <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                    row.statut === 'disponible' ? 'bg-green-100 text-green-800' :
                    row.statut === 'attribué' ? 'bg-blue-100 text-blue-800' :
                    'bg-gray-100 text-gray-800'
                }`}>
                    {row.statut}
                </span>
            )
        },
    ];

    return (
        <div className="p-8">
            <div className="flex justify-between items-center mb-8">
                <h1 className="text-2xl font-bold text-gray-900">Materials</h1>
                <button className="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <Plus className="w-5 h-5 mr-2" />
                    Add Material
                </button>
            </div>

            <DataTable
                columns={columns}
                data={data}
                pagination={pagination}
                onPageChange={fetchData}
                loading={loading}
            />
        </div>
    );
};

export default Materials;
