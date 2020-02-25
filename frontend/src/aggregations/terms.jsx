import React, { memo } from "react";
import { BarChart, Bar, Cell, XAxis, YAxis, CartesianGrid, Tooltip, Legend } from "recharts";

/**
 * Représentation d'une aggrégation Terms
 */
function Terms(props) {
    const { aggregation } = props;
    const data = [];
    for (const option in aggregation) {
        data.push({ option: option, total: aggregation[option] });
    }
    data.sort((a, b) => {
        if (a.total === b.total) return 0;
        return a.total < b.total ? -1 : 1;
    });
    return (
        <div data-cy="aggregation-terms">
            <BarChart
                width={1000}
                height={300}
                data={data}
                margin={{
                    top: 5,
                    right: 30,
                    left: 20,
                    bottom: 5,
                }}
            >
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="option" />
                <YAxis />
                <Tooltip />
                <Legend />
                <Bar dataKey="total" fill="#8884d8" />
            </BarChart>
        </div>
    );
}
export default memo(Terms);
