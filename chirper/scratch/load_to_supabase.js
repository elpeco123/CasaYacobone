import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import pg from 'pg';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const { Client } = pg;

async function main() {
    console.log('Conectando a Supabase PostgreSQL...');

    const client = new Client({
        host: 'aws-0-us-east-2.pooler.supabase.com',
        port: 6543,
        database: 'postgres',
        user: 'postgres.yvdzqvhnpjmmmqyagbfz',
        password: 'FRANCHESCO.123',
        ssl: {
            rejectUnauthorized: false
        }
    });

    try {
        await client.connect();
        console.log('✅ Conexión establecida con éxito con Supabase!');

        const sqlFilePath = path.join(__dirname, '..', 'simular_datos_supabase.sql');
        console.log(`Leyendo archivo SQL: ${sqlFilePath}...`);
        const sql = fs.readFileSync(sqlFilePath, 'utf8');

        console.log('Ejecutando script SQL masivo en Supabase (Proveedores, 20 Productos, 12 Meses de Ventas)...');
        await client.query(sql);

        console.log('🎉 ¡Script SQL ejecutado con éxito en Supabase!');

        // Query verification stats
        const provRes = await client.query('SELECT COUNT(*) FROM proveedores;');
        const prodRes = await client.query('SELECT COUNT(*) FROM productos;');
        const ventRes = await client.query('SELECT COUNT(*) FROM ventas;');
        const itemRes = await client.query('SELECT COUNT(*) FROM venta_items;');

        console.log('\n📊 ESTADÍSTICAS EN SUPABASE:');
        console.log(`- Proveedores: ${provRes.rows[0].count}`);
        console.log(`- Productos: ${prodRes.rows[0].count}`);
        console.log(`- Ventas totales: ${ventRes.rows[0].count}`);
        console.log(`- Items de venta: ${itemRes.rows[0].count}`);

    } catch (err) {
        console.error('❌ Error al ejecutar SQL en Supabase:', err);
    } finally {
        await client.end();
        console.log('Conexión cerrada.');
    }
}

main();
