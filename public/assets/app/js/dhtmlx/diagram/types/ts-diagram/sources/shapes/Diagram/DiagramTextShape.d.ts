import { IFlowShapeTextConfig, IShape } from "../../types";
import { BaseShape } from "../Base";
export declare class DiagramTextShape extends BaseShape implements IShape {
    config: IFlowShapeTextConfig;
    private _oldText;
    constructor(config: IFlowShapeTextConfig, parameters?: any);
    render(): any;
    getMetaInfo(): any[];
    protected setDefaults(config: IFlowShapeTextConfig, defaults: IFlowShapeTextConfig): IFlowShapeTextConfig;
    protected getContent(): any;
}
