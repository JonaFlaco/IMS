import { IBoxSize, ICoords, ILinkConfig } from "../../types";
import { BaseShape } from "../Base";
export declare class Line extends BaseShape {
    config: ILinkConfig;
    constructor(config: ILinkConfig, defaults?: any);
    isConnector(): boolean;
    getMetaInfo(): any[];
    setDefaults(config: ILinkConfig): ILinkConfig;
    render(): string;
    getBox(): IBoxSize;
    protected _getType(): string;
    protected _getPoints(): string;
    protected _getStringPoints(): string;
    protected _getArrowLine(): any[];
    protected _angleArrow(from: ICoords, to: ICoords): any;
    protected _arrow(from: ICoords, to: ICoords): string;
}
