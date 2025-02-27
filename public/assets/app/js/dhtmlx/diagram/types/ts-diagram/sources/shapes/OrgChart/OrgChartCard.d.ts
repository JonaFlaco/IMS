import { IOrgChartConfig, IShape } from "../../types";
import { BaseShape } from "../Base";
export declare class OrgChartCard extends BaseShape implements IShape {
    config: IOrgChartConfig;
    constructor(config: IOrgChartConfig, parameters?: any);
    render(): string;
    getMetaInfo(): any[];
    protected getCss(): string;
    protected setDefaults(config: IOrgChartConfig, defaults?: IOrgChartConfig): IOrgChartConfig;
    protected getContent(): any;
}
