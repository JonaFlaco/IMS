import { IEventSystem } from "../../ts-common/events";
import { View } from "../../ts-common/view";
import { DataEvents, Diagram, DiagramEvents, SelectionEvents } from "../../ts-diagram";
import { Layout } from "../../ts-layout";
import { DiagramEditorEvents, IDiagramEditorHandlersMap, IEditorConfig } from "./types";
import { Controls } from "./controls/Controls";
import { Sidebar } from "./Sidebar";
import { UndoManager } from "./UndoManager";
import { SelectionBox } from "./SelectionBox";
import { BlockSelection } from "./BlockSelection";
export declare abstract class BaseDiagramEditor extends View {
    version: string;
    config: IEditorConfig;
    events: IEventSystem<DataEvents | SelectionEvents | DiagramEvents | DiagramEditorEvents, IDiagramEditorHandlersMap>;
    diagram: Diagram;
    history: UndoManager;
    protected _layout: Layout;
    protected _sidebar: Sidebar;
    protected _resizer: Controls;
    protected _selectionBox: SelectionBox;
    protected _blockSelection: BlockSelection;
    constructor(container: string | HTMLElement, config: IEditorConfig);
    paint(): void;
    import(diagram: Diagram): void;
    parse(data: any[]): void;
    serialize(): any;
    getRootView(): any;
    protected abstract _initUI(container: any): any;
    protected abstract _initDiagram(): any;
    protected _setHandlers(): void;
    protected _checkEditMode(): void;
    protected _removeShape(id: string): void;
    protected _initHotkeys(): void;
    protected _getDefaults(): {
        text: {
            text: string;
        };
    };
    protected _importFile(data: any): void;
    private _removeSelected;
    private _itemsHandlerClick;
}
