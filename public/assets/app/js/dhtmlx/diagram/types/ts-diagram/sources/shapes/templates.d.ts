import { IFlowShapeConfig, IOrgChartConfig, ICustomShapeConfig } from "../types";
export declare function getCircleTpl(config: IFlowShapeConfig | IOrgChartConfig | ICustomShapeConfig): string;
export declare function getHeaderTpl(config: IOrgChartConfig): any;
export declare function getTextTemplate(config: IOrgChartConfig, content: any): any;
export declare function getShapeCss(config: IFlowShapeConfig): {
    width: number;
    height: number;
    display: string;
    "flex-direction": string;
    "justify-content": string;
    "text-align": "left" | "center" | "right";
    "line-height": number;
    "font-size": number;
    "font-style": "normal" | "italic" | "oblique";
    "font-weight": string;
    color: string;
    "word-break": string;
    "white-space": string;
};
