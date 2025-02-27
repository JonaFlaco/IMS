import { BaseDiagramEditor } from "./DiagramEditor";
export declare class DiagramEditor extends BaseDiagramEditor {
    protected _setHandlers(): void;
    protected _initDiagram(): void;
    protected _initUI(container: any): void;
    protected _initHotkeys(): void;
    protected _getDefaults(): {
        text: {
            text: string;
        };
    };
}
