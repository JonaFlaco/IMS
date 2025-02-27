import { IOrgChartConfig, IShape } from "../../types";
import { OrgChartCard } from "./OrgChartCard";
export declare class OrgChartImgCard extends OrgChartCard implements IShape {
    getMetaInfo(): any[];
    protected setDefaults(config: IOrgChartConfig, defaults?: IOrgChartConfig): IOrgChartConfig;
    protected getCss(): string;
    protected getContent(): any;
}
